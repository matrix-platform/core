<?php //>

namespace matrix\core;

use matrix\utility\ValueObject;

trait Handler {

    use ValueObject;

    protected function handle() {
        $this->init();

        $form = $this->trim($this->wrap());
        $result = $this->validate($form);
        $success = false;

        if ($result) {
            $view = $this->validationView() ?: cfg('default.validation-view');
        } else {
            $form = $this->preprocess($form);
            $tx = $this->transaction();

            if ($tx) {
                $tx->begin();
            }

            try {
                $result = $this->process($form);
            } catch (AppException $exception) {
                $result = ['error' => $exception->getMessage()];
            } finally {
                $success = @$result['success'];

                if ($success) {
                    if ($tx) {
                        $tx->commit();
                    }

                    $result = $this->postprocess($form, $result);
                } else {
                    if ($tx) {
                        $tx->rollback();
                    }
                }
            }

            $view = @$result['view'];

            if (!$view) {
                if ($success) {
                    $view = $this->view() ?: cfg('default.success-view');
                } else {
                    $view = $this->errorView() ?: cfg('default.error-view');
                }
            }
        }

        if (!$success) {
            $this->cleanup();
        }

        resolve($view)->render($this, $form, $result);
    }

    protected function init() {
    }

    protected function wrap() {
        return null;
    }

    protected function validate($form) {
        return null;
    }

    protected function preprocess($form) {
        return $form;
    }

    protected function process($form) {
        return ['success' => true];
    }

    protected function postprocess($form, $result) {
        return $result;
    }

    protected function cleanup() {
    }

    private function trim($value) {
        if (is_string($value)) {
            $value = trim($value);

            return strlen($value) ? $value : null;
        }

        if (is_array($value)) {
            return array_map([$this, 'trim'], $value);
        }

        return $value;
    }

}
