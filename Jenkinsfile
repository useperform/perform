pipeline {
    agent any

    triggers {
        pollSCM('H/3 * * * *')
    }

    stages {
        stage('build') {
            steps {
                sh 'make clean'
                sh 'make build'
            }
        }
        stage('test') {
            steps {
                sh 'make test_publish'
            }
        }
    }

    post {
        always {
            junit 'test_results.xml'
        }
        failure {
            mail to: 'me@glynnforrest.com',
            subject: "Failed Pipeline: ${currentBuild.fullDisplayName}",
            body: "See ${env.BUILD_URL}"
        }
    }
}
