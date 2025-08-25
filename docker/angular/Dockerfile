FROM node:20-alpine

WORKDIR /app

COPY frontend/app/package*.json ./

RUN npm install -g @angular/cli && npm install

COPY frontend/app .

EXPOSE 4200

CMD ["npm", "start"]