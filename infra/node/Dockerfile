# Use full Debian-based Node.js LTS
FROM node:20-bullseye

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy app files
COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev"]
