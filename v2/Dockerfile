# Build stage
FROM golang:1.20.1-alpine3.17 as build


WORKDIR /usr/src/app

COPY go.mod go.sum ./

RUN go mod download && go mod verify

COPY . .

RUN if test -e app.env; then echo 'found app.env'; else mv app-sample.env app.env; fi; \
    go build -v -o /dist/vesicash-notifications-ms

# Deployment stage
FROM alpine:3.17

WORKDIR /usr/src/app

COPY --from=build /usr/src/app ./

COPY --from=build /dist/vesicash-notifications-ms /usr/local/bin/vesicash-notifications-ms


CMD ["vesicash-notifications-ms"]
