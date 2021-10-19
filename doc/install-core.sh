folder='vendor/matrix-platform/core'

cp -R ${folder}/www .
cp ${folder}/doc/htaccess www/.htaccess
[ ! -f config.php ] && cp ${folder}/doc/config-sample.php .
[ ! -f .gitignore ] && cp ${folder}/doc/gitignore .gitignore

mkdir -p cfg class controller/backend data doc files i18n logs menu table view/{native,twig} www/files
chmod 777 data files logs www/files

mkdir -p www/backend www/{cn,en,tw}/backend
