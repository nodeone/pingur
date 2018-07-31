set -ex
USERNAME=ewertthedragon
BRANCH=latest
# image name
IMAGE=pingur
ORG=digitalist
docker build -t $ORG/$IMAGE:$BRANCH .
docker tag $ORG/$IMAGE:$BRANCH $ORG/$IMAGE:$BRANCH
docker push $ORG/$IMAGE:$BRANCH
