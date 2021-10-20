<?php

declare(strict_types=1);

namespace prime\tests\unit\models\forms;

use Codeception\Test\Unit;
use prime\interfaces\WorkspaceForNewOrUpdateFacility;
use prime\models\forms\UpdateFacility;
use prime\tests\_helpers\AllAttributesMustHaveLabels;
use prime\tests\_helpers\AllFunctionsMustHaveReturnTypes;
use prime\tests\_helpers\AttributeValidationByExample;
use prime\tests\_helpers\ModelTestTrait;
use prime\tests\_helpers\YiiLoadMustBeDisabled;
use prime\values\FacilityId;

/**
 * @covers \prime\models\forms\UpdateFacility
 */
class UpdateFacilityTest extends Unit
{
    use AllFunctionsMustHaveReturnTypes;
    use AttributeValidationByExample;
    use YiiLoadMustBeDisabled;

    private function getWorkspace(): WorkspaceForNewOrUpdateFacility
    {
        return $this->getMockBuilder(WorkspaceForNewOrUpdateFacility::class)->getMock();
    }
    private function getModel(): UpdateFacility
    {
        return new UpdateFacility(new FacilityId("1"), $this->getWorkspace());
    }

    public function testGetId(): void
    {
        $id = new FacilityId("1");
        $workspace = $this->getWorkspace();

        $model = new UpdateFacility($id, $workspace);

        // We care about the value, not the instance.
        $this->assertSame($id->getValue(), $model->getId()->getValue());
    }


    public function testGetWorkspace(): void
    {
        $id = new FacilityId("1");
        $workspace = $this->getWorkspace();

        $model = new UpdateFacility($id, $workspace);

        $this->assertSame($workspace, $model->getWorkspace());
    }

    public function validSamples(): iterable
    {
        yield [
            [
                'data' => [
                    'name' => 'cool stuff',
                    'alternative_name' => 'test',
                    'code' => 'code',
                    'coordinates' => '(14, 5)'
                ]
            ]
        ];

        yield [
            [
                'data' => [
                    'name' => 'cool stuff'
                ]
            ]
        ];
    }

    public function invalidSamples(): iterable
    {
        return [];
//        yield [
//            [
//                'coordinates' => 'wrong',
//                'alternative_name' => 'ac',
//                'code' => 'ab'
//            ]
//        ];
    }
}
