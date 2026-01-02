FROM nginx:alpine

RUN apk --no-cache upgrade

COPY default.conf /etc/nginx/conf.d/default.conf

COPY ./error-pages /etc/nginx/error-pages

WORKDIR /srv/app