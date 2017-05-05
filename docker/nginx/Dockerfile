FROM nginx:mainline-alpine

# adding tini : a tiny but valid init for containers (https://github.com/krallin/tini)
# the idea is to be able pass signals to nginx
RUN apk add --no-cache tini

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["nginx"]
