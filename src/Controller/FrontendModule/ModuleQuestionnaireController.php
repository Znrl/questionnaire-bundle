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
use Contao\Email;
use Contao\ModuleModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Znrl\QuestionnaireBundle\Form\QuestionnaireForm;
use Znrl\QuestionnaireBundle\Form\FormHelper;
use Znrl\QuestionnaireBundle\Form\SendQuestionnaireForm;
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

        $arrQuestionnaireItems = [];

        foreach ($objQuestionnaireItems as $objQuestionnaireItem) {
            $arrQuestionnaireItems[] = (object) $objQuestionnaireItem->row();
        }

        $arrQuestionnaireItems = array_values(array_filter($arrQuestionnaireItems));
        $sessionFormData = $this->getFormDataFromSession($model->questionnaire);
        $form = QuestionnaireForm::createForm($model->questionnaire, $arrQuestionnaireItems, $sessionFormData);
        $formData = FormHelper::validateForm($form) ? $form->fetchAll() : $sessionFormData;

        if ([] !== $formData) {

            $score = $this->calculateScoreFromAnswers($arrQuestionnaireItems, $formData);
            $this->saveFormDataToSession($model->questionnaire, $formData);
            $arrQuestionnaireResults = $this->getResultsByScore($model->questionnaire, $score);

            $sendForm = SendQuestionnaireForm::createForm($model->questionnaire);
            if (FormHelper::validateForm($sendForm)) {
                $this->sendResultsByMail($sendForm->fetch('email'), $arrQuestionnaireItems, $formData, $arrQuestionnaireResults[0]);
            }
            $template->set('send_form', FormHelper::generateForm($sendForm));
            $template->set('score', $score);
            $template->set('result_title', $arrQuestionnaireResults[0]->title);
            $template->set('result_text', $arrQuestionnaireResults[0]->resultText);
        }
        $template->set('r', $request);

        $template->set('result', [] !== $formData);
        $template->set('form', FormHelper::generateForm($form));

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

    private function sendResultsByMail($email, $arrQuestionnaireItems, $formData, $result): void
    {
        $email = new Email();
        $email->subject = '';
        $email->text = '';

        foreach ($arrQuestionnaireItems as $item) {
            $email->text  .= $item->question;
            $email->text  .= `\n`;
            $email->text  .= $formData['answer_' . $item->id];
            $email->text  .= `\n\n`;
        }

        $email->sendTo('lionel@richie.com');
    }

    private function getResultsByScore($questionnaireId, $score): array
    {
        $objQuestionnaireResults = QuestionnaireResultModel::findPublishedByPidAndMinScore($questionnaireId, $score);

        $arrQuestionnaireResults = [];

        foreach ($objQuestionnaireResults as $objQuestionnaireResult) {
            $arrQuestionnaireResults[] = (object) $objQuestionnaireResult->row();
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