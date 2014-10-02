<?php
/**
 * @file
 * Contains \Drupal\geocoder\Form\APIKeysForm.
 */
namespace Drupal\geocoder\Form;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @TODO: Write something here
 */
class APIKeysForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'geocoder_api_keys';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('geocoder.google');

    $form['google_api_key'] = array(
      '#type' => 'textfield',
      '#title' => $this->t("Google API Key"),
      '#default_value' => $config->get('api_key')
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('geocoder.google')
      ->set('api_key', $form_state->getValue('google_api_key'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}