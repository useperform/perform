pipeline {
    agent any

    stages {
        stage('build') {
            steps {
                sh 'docker run -i --rm -u $(id -u):$(id -g) -v $PWD:/app php:7.2-cli /app/bin/generate-root-composer-config.php'
                sh 'test -d ~/.composer || mkdir -v ~/.composer'
                sh 'docker run -i --rm -u $(id -u):$(id -g) -v $PWD:/app -v ~/.composer:/tmp composer update --profile --ignore-platform-reqs'
            }
        }
        stage('test') {
            steps {
                sh 'docker run -i --rm -u $(id -u):$(id -g) -v $PWD:/app -w /app php:7.2-cli /app/vendor/bin/phpunit --log-junit test_results.xml'
            }
        }
    }

    post {
        always {
            junit 'test_results.xml'
        }
    }
}
