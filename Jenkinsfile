pipeline {
    agent any

    stages {
        stage('lint') {
            steps {
                sh 'docker run -i --rm -v $PWD:/app milchundzucker/php-parallel-lint:latest /app/src /app/bin'
            }
        }
        stage('build-image') {
            steps {
                sh 'docker build -t perform --build-arg PHP_VERSION=7.1 .'
                sh 'docker run -i --rm perform php -v'
            }
        }
        stage('build') {
            steps {
                sh 'docker run -i --rm -u $(id -u):$(id -g) -v $PWD:/code perform bin/generate-root-composer-config.php'
                sh 'test -d ~/.composer || mkdir -v ~/.composer'
                sh 'docker run -i --rm -u $(id -u):$(id -g) -v $PWD:/app -v ~/.composer:/tmp composer update --profile --ignore-platform-reqs'
            }
        }
        stage('test') {
            steps {
                sh 'docker run -i --rm -u $(id -u):$(id -g) -v $PWD:/code perform vendor/bin/phpunit --log-junit test_results.xml'
            }
        }
    }

    post {
        always {
            junit 'test_results.xml'
        }
    }
}
