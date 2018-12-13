git add -A
echo Introduce el commit a subir\:
read commit
git commit -a -m $commit
git push
