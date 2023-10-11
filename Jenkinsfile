pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build and Test') {
            steps {
                sh 'composer install'
                sh 'php artisan test'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    def dockerImage = docker.build("my-laravel-app")
                }
            }
        }

        stage('Deploy to Docker') {
            steps {
                sh 'docker-compose up -d'
            }
        }

        stage('Deploy Nginx') {
            steps {
                sh 'docker-compose -f nginx/docker-compose.yml up -d'
            }
        }
    }
}
