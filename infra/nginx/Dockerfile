FROM nginx:alpine

RUN apk --no-cache upgrade

COPY default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/app