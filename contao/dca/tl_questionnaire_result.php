<?php


declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */

use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

$GLOBALS['TL_DCA']['tl_questionnaire_result'] = array
(
    // Config
    'config' => array
    (
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_questionnaire',
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid,published,minScore' => 'index',
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode' => DataContainer::MODE_PARENT,
            'fields' => array('sorting'),
            'panelLayout' => 'search,filter,limit',
            'defaultSearchField' => 'title',
            'headerFields' => array('title', 'headline', 'tstamp'),
            'renderAsGrid' => true,
            'limitHeight' => 160
        ),
        'label' => array
        (
            'fields' => array('title'),
            'format' => '%s'
        ),
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,headline,resultText,minScore;{publish_legend},published'
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'autoincrement' => true)
        ),
        'pid' => array
        (
            'foreignKey' => 'tl_questionnaire.title',
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0),
            'relation' => array('type' => 'belongsTo', 'load' => 'lazy')
        ),
        'sorting' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0)
        ),
        'tstamp' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0)
        ),
        'title' => array
        (
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => array('type' => 'string', 'length' => 255, 'default' => '')
        ),
        'headline' => array
        (
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => array('type' => 'string', 'length' => 255, 'default' => '')
        ),
        'resultText' => array
        (
            'search'                  => true,
            'inputType'               => 'textarea',
            'eval'                    => array('mandatory'=>true, 'basicEntities'=>true, 'rte'=>'tinyMCE', 'helpwizard'=>true, 'tl_class'=>'clr'),
            'explanation'             => 'insertTags',
            'sql'                     => array('type'=>'text', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_MEDIUMTEXT, 'notnull'=>false)
        ),
        'minScore' => array
        (
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'nospace'=> true, 'tl_class' => 'w50'),
            'sql' => array('type' => 'float', 'unsigned' => true, 'default' => '0')
        ),
        'published' => array
        (
            'toggle' => true,
            'filter' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType' => 'checkbox',
            'eval' => array('doNotCopy' => true),
            'sql' => array('type' => 'boolean', 'default' => false)
        )
    )
);