FROM node:20-alpine

WORKDIR /src

COPY package*.json ./
# Устанавливаем зависимости + tailwind вручную на всякий случай
RUN npm install && npm install @nuxtjs/tailwindcss

COPY . .

ENV HOST=0.0.0.0
ENV PORT=3000
ENV NODE_ENV=development

CMD npm run dev:prepare && npx nuxi dev playground --host 0.0.0.0
