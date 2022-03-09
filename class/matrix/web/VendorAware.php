<?php //>

namespace matrix\web;

trait VendorAware {

    use vendor\RememberMe;

    private $vendor;

    public function vendor() {
        if ($this->vendor === null) {
            $this->vendor = false;

            $vendor = $this->get('Vendor') ?: $this->recall();

            if ($vendor) {
                $current = @$vendor['original_user'] ? model('Vendor')->get($vendor['id']) : model('Vendor')->queryById($vendor['id']);

                if ($current && $current['password'] === $vendor['password']) {
                    $this->vendor = $current;

                    $this->set('Vendor', $current);
                } else {
                    $this->remove('Vendor');
                }
            }
        }

        return $this->vendor;
    }

}
