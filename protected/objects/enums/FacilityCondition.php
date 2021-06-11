<?php
declare(strict_types=1);

namespace prime\objects\enums;

/**
 * @method static self unknown()
 * @method static self notDamaged()
 * @method static self slightlyDamaged()
 * @method static self substantiallyDamaged()
 * @method static self fullyDamaged()
 * @method static self notRelevant()
 */
class FacilityCondition extends Enum
{
    /**
     * @codeCoverageIgnore
     */
    protected static function values(): array
    {
        return [
            'unknown' => 'unknown',
            'notDamaged' => 'A1',
            'slightlyDamaged' => 'A2',
            'substantiallyDamaged' => 'A3',
            'fullyDamaged' => 'A4',
            'notRelevant' => 'A5',
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    protected static function labels(): array
    {
        return [
            'unknown' => \Yii::t('app', 'Unknown'),
            'notDamaged' => \Yii::t('app', 'Not damaged'),
            'slightlyDamaged' => \Yii::t('app', 'Slightly damaged'),
            'substantiallyDamaged' => \Yii::t('app', 'Substantially damaged'),
            'fullyDamaged' => \Yii::t('app', 'Fully damaged'),
            'notRelevant' => \Yii::t('app','Not relevant'),
        ];
    }
}
