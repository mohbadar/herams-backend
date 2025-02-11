<?php


namespace prime\components;

use prime\exceptions\SurveyDoesNotExist;
use SamIT\LimeSurvey\Interfaces\LocaleAwareInterface;
use SamIT\LimeSurvey\Interfaces\ResponseInterface;
use SamIT\LimeSurvey\Interfaces\SurveyInterface;
use SamIT\LimeSurvey\Interfaces\TokenInterface;
use SamIT\LimeSurvey\JsonRpc\Client;
use yii\base\Component;
use yii\caching\CacheInterface;
use yii\di\Instance;

class LimesurveyDataProvider extends Component
{
    /** @var Client */
    public $client = 'limesurvey';

    public function init()
    {
        parent::init();
        $this->client = Instance::ensure($this->client, Client::class);
    }

    public function createToken(
        int $surveyId,
        string $token
    ) {
        return $this->client->createToken($surveyId, [
            'token' => $token
        ]);
    }


    public function getToken(int $surveyId, string $token)
    {
        return $this->client->getToken($surveyId, $token);
    }

    /**
     * Get all responses in a survey and store them in the cache.
     * This function never uses the cache for reading.
     * @param int $surveyId
     * @return iterable|ResponseInterface[]
     */
    public function refreshResponsesByToken(int $surveyId, string $token): iterable
    {
        /**
         * @var ResponseInterface $value
         */
        foreach ($this->client->getResponsesByToken($surveyId, $token) as $key => $value) {
            if ($value->getSubmitDate() !== null) {
                yield $value;
            }
        }
    }

    public function getSurvey(int $surveyId): SurveyInterface
    {
        try {
            $survey = $this->client->getSurvey($surveyId);
            if ($survey instanceof LocaleAwareInterface && in_array(substr(\Yii::$app->language, 0, 2), $survey->getLanguages())) {
                $survey = $survey->getLocalized(substr(\Yii::$app->language, 0, 2));
            }
            return $survey;
        } catch (\Exception $e) {
            throw SurveyDoesNotExist::fromClient($e) ?? $e;
        }
    }

    public function getUrl(int $surveyId, array $params = []): string
    {
        return $this->client->getUrl($surveyId, $params);
    }

    public function listSurveys(): array
    {
        return $this->client->listSurveys();
    }

    /** @return TokenInterface[] */
    public function getTokens(int $surveyId): array
    {
        try {
            return $this->client->getTokens($surveyId);
        } catch (\Exception $e) {
            // Clear the cache, this uses the cache key getSurveyProperties.
            $key = Client::class . 'getSurveyProperties' . serialize([$surveyId, ['attributedescriptions']]);
            /** @var CacheInterface $cache */
            $cache = app()->limesurveyCache;
            $cache->delete($key);
            throw $e;
        }
    }
}
