<?php //>

return new Twig\TwigFilter('alt', function ($image) {
    if ($image && !preg_match('/^data:/', $image)) {
        $data = load_file_data($image);

        if ($data) {
            return $data['description'];
        }
    }

    return '';
});
