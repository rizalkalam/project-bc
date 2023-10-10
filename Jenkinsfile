pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                // Langkah ini akan mengambil kode dari repositori Git
                checkout scm
            }
        }
        
        stage('Install Composer') {
            steps {
                // Langkah ini akan menginstal Composer
                sh 'sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer'
            }
        }
        
        stage('Install Dependencies') {
            steps {
                // Langkah ini akan menjalankan 'composer install' untuk menginstal semua dependencies Laravel
                sh 'composer install'
            }
        }
        
        // Tambahkan langkah-langkah lain sesuai dengan kebutuhan proyek Anda
    }
}
