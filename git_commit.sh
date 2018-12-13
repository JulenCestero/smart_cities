git pull #bajar lo que hay en el git
git add -A #los archivos cambiados los guarda para luego subirlos
echo Introduce el commit a subir\: #el comentario de lo que voy a subir
read commit
git commit -a -m "$commit"  #genera una version nuevo con los cambios y el comentario que he puesto en commit
git push #sube a git , introducir user y password
