## Usage

### Build Docker image
Open the root directory and paste: ```docker compose up -d --build```

### Create Database
1. Open docker app using: ```docker exec -it backend_php bash```
2. Navigate to app folder using: ```cd app```
3. Run migration: ```make run-migrations```

### Access
1.App - localhost:8000
2.Database adminer - localhost:8001
