<?php //>

namespace matrix\web;

trait UserAware {

    private $user;

    public function user() {
        if ($this->user === null) {
            $this->user = false;

            $user = $this->get('User');

            if ($user) {
                $current = model('User')->queryById($user['id']);

                if ($current && $current['password'] === $user['password']) {
                    $this->user = $current;

                    $this->set('User', $current);
                } else {
                    $this->remove('User');
                }
            }
        }

        return $this->user;
    }

    protected function loadSetting() {
        $user = $this->user();

        if ($user) {
            $file = create_folder(APP_DATA . 'setting/') . $user['id'];

            if (file_exists($file)) {
                return json_decode(file_get_contents($file), true);
            }
        }

        return [];
    }

}
