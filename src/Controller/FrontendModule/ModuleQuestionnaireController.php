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
use Contao\CoreBundle\String\SimpleTokenParser;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Email;
use Contao\Environment;
use Contao\ModuleModel;
use Contao\System;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
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

            if ($model->questionnaire_allow_send_mail) {

                $sendForm = SendQuestionnaireForm::createForm($model->questionnaire);

                if (FormHelper::validateForm($sendForm)) {

                    $mailContent = $this->prepareMailContent(
                        $model->questionnaire_mail_text,
                        $sendForm->fetch('name'),
                        $sendForm->fetch('email'),
                        $arrQuestionnaireItems,
                        $formData,
                        $score,
                        $arrQuestionnaireResults[0]);

                    $this->sendResultsByMail(
                        $model->mailRecipient,
                        $model->mailSubject,
                        $model->questionnaire_send_mail_bcc ? $sendForm->fetch('email') : '',
                        $mailContent
                    );
                }
                $template->set('send_form', FormHelper::generateForm($sendForm));
            }

            $template->set('score', $score);
            $template->set('result_headline', $arrQuestionnaireResults[0]->headline);
            $template->set('result_text', $arrQuestionnaireResults[0]->resultText);
        }
        $template->set('r', $request);

        $template->set('result', [] !== $formData);
        $template->set('form', FormHelper::generateForm($form));

        return $template->getResponse();
    }

    private function calculateScoreFromAnswers(array $arrQuestionnaireItems, array $formData): float
    {
        $score = 0;
        foreach ($arrQuestionnaireItems as $item) {
            $score += $item->weightingFactor * $formData['answer_' . $item->id];
        }

        return $score;
    }

    private function prepareMailContent(string $mailText, string $name, string $email, array $arrQuestionnaireItems, array $formData, float $score, object $result): string
    {
        $formDataText ='';
        foreach ($arrQuestionnaireItems as $item) {
            $formDataText  .= $item->question;
            $formDataText  .= '\n';
            $formDataText  .= $formData['answer_' . $item->id];
            $formDataText  .= '\n\n';
        }

        $arrTokens = [
            'q_name' => $name,
            'q_email' => $email,
            'q_result_title' => $result->title,
            'q_result_text' => $result->resultText,
            'q_score' => $score,
            'q_form_data' => $formDataText
        ];

        $parser = new SimpleTokenParser(new ExpressionLanguage());

        return $parser->parse($mailText, $arrTokens);
    }

    private function sendResultsByMail(string $mailRecipient, string $subject, string $emailBcc, string $mailContent): void
    {

        $mail = new Email();
        $mail->subject = $subject;
        $mail->text = $mailContent;

        $emailBcc !== '' ?? $mail->sendBcc($emailBcc);

        $mail->sendTo($mailRecipient);
    }

    private function getResultsByScore(int $questionnaireId, float $score): array
    {
        $objQuestionnaireResults = QuestionnaireResultModel::findPublishedByPidAndMinScore($questionnaireId, $score);

        $arrQuestionnaireResults = [];

        foreach ($objQuestionnaireResults as $objQuestionnaireResult) {
            $arrQuestionnaireResults[] = (object) $objQuestionnaireResult->row();
        }

        return $arrQuestionnaireResults;
    }

    private function saveFormDataToSession(int $questionnaireId, array $formData): void
    {
        $this->getSession()->set(self::SESSION_KEY, [$questionnaireId => $formData]);
    }

    private function getFormDataFromSession(int $questionnaireId): array
    {
        $data = $this->getSession()->get(self::SESSION_KEY, []);

        return $data[$questionnaireId] ?? [];
    }


    private function getSession(): SessionInterface
    {
       return System::getContainer()->get('request_stack')->getCurrentRequest()->getSession();
    }
}