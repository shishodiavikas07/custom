<?php

namespace Drupal\custom_module\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

class NewSiteInformationForm extends SiteInformationForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_config = $this->config("system.site");
    $form = parent::buildForm($form, $form_state);
    $site_api_key = $site_config->get("siteapikey");
    $form["site_information"]["siteapikey"] = [
      "#type" => "textfield",
      "#title" => t("Site API Key"),
      "#default_value" => $site_api_key ?: "No API Key yet",
      "#description" => t("Custom field to set the API Key"),
    ];
    if (!empty($site_api_key)) {
      $form["actions"]["submit"]["#value"] = t("Update Configuration");
    }
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $site_api_key = $form_state->getValue("siteapikey");
    $this->config("system.site")
      ->set("siteapikey", $site_api_key)
      ->save();
    parent::submitForm($form, $form_state);
    if (empty($site_api_key)) {
      $site_api_key = "NULL";
    }
    \Drupal::messenger()->addMessage(
      "Site API Key has been saved with " . $site_api_key
    );
  }
}
