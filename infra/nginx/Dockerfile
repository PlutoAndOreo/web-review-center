FROM nginx:alpine

COPY default.conf /etc/nginx/conf.d/default.conf

RUN apt update && apt upgrade -y
RUN apt install certbot python3-certbot-nginx -y
