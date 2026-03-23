<?php

declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */

namespace Znrl\QuestionnaireBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Znrl\QuestionnaireBundle\Form\QuestionnaireForm;
use Znrl\QuestionnaireBundle\Model\QuestionnaireItemModel;
use Znrl\QuestionnaireBundle\Model\QuestionnaireResultModel;

#[AsFrontendModule(type: 'questionnaire', category: 'questionnaire', template: 'frontend_module/questionnaire')]
class ModuleQuestionnaireController extends AbstractFrontendModuleController
{

    private const string SESSION_KEY = 'znrl_questionnaire';

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $objQuestionnaireItems = QuestionnaireItemModel::findPublishedByPid($model->questionnaire);

        if ($objQuestionnaireItems === null) {

            return new Response();
        }

        $arrQuestionnaireItems = array();

        foreach ($objQuestionnaireItems as $objQuestionnaireItem) {
            $objTemp = (object) $objQuestionnaireItem->row();
            $arrQuestionnaireItems[] = $objTemp;
        }

        $arrQuestionnaireItems = array_values(array_filter($arrQuestionnaireItems));

        $form = QuestionnaireForm::createQuestionnaireForm($model->questionnaire, $arrQuestionnaireItems, $this->getFormDataFromSession($model->questionnaire));

        $template->set('result', false);

        if (QuestionnaireForm::validateQuestionnaireForm($form)) {

            $template->set('result', true);
            $formData = $form->fetchAll();
            $score = $this->calculateScoreFromAnswers($arrQuestionnaireItems, $formData);
            $this->saveFormDataToSession($model->questionnaire, $formData);
            $arrQuestionnaireIResults = $this->getResultsByScore($model->questionnaire, $score);
            $template->set('result_title', $arrQuestionnaireIResults[0]->title);
            $template->set('result_text', $arrQuestionnaireIResults[0]->resultText);

        }

        $template->set('form', QuestionnaireForm::generateQuestionnaireForm($form));

        return $template->getResponse();
    }

    private function calculateScoreFromAnswers($arrQuestionnaireItems, $formData): float
    {
        $score = 0;
        foreach ($arrQuestionnaireItems as $item) {
            $score += $item->weightingFactor * $formData['answer_' . $item->id];
        }

        return $score;
    }

    private function getResultsByScore($questionnaireId, $score): array
    {
        $objQuestionnaireResults = QuestionnaireResultModel::findPublishedByPidAndMinScore($questionnaireId, $score);

        $arrQuestionnaireResults = array();

        foreach ($objQuestionnaireResults as $objQuestionnaireResult) {
            $objTemp = (object) $objQuestionnaireResult->row();
            $arrQuestionnaireResults[] = $objTemp;
        }

        return $arrQuestionnaireResults;
    }

    private function saveFormDataToSession($questionnaireId, $formData): void
    {
        $this->getSession()->set(self::SESSION_KEY, [$questionnaireId => $formData]);
    }

    private function getFormDataFromSession($questionnaireId): array
    {
        $data = $this->getSession()->get(self::SESSION_KEY, []);

        return $data[$questionnaireId] ?? [];
    }


    private function getSession(): SessionInterface
    {
       return System::getContainer()->get('request_stack')->getCurrentRequest()->getSession();
    }
}