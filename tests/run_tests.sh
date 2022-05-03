#!/bin/bash

function printHelp {
    echo "Load fixtures and run tests"
    echo "Usage: runTests.sh [options] [directory]"
    echo ""
    echo "Options:"
    echo "   -r|--reset resets the sqlite test database before testing"
    echo "   -h|--help  print this help"
    echo ""
    echo "Directory:"
    echo "   Optionally you can supply a directory. In that case, all tests inside that"
    echo "   directory and its subdirectories will be run INSTEAD of the ones specified"
    echo "   inside app/phpunit.xml"
    echo ""
    echo "Examples:"
    echo "   Run the tests as specified inside app/phpunit.xml:"
    echo "      ./runTests.sh"
    echo "   Run the tests as specified inside app/phpunit.xml, but reset database first:"
    echo "      ./runTests.sh -r"
    echo "   Run the tests from src/AcmeBundle:"
    echo "      ./runTests.sh src/AcmeBundle"
    echo "   Run the tests from src/AcmeBundle, but reset database first:"
    echo "      ./runTests.sh -r src/AcmeBundle"
    echo "   Run only the service tests from src/AcmeBundle:"
    echo "      ./runTests.sh -r src/AcmeBundle/Tests/Service"
    exit 0
}

DIRECTORY=""

# parse command line arguments
for key in $@
do
case ${key} in
    -h|--help)
    printHelp
    ;;
    *)
    DIRECTORY=${key}
    ;;
esac
done

# load fixtures and run tests
echo "y" | php ../vendor/bin/phpunit /home/govidiu/myproject/internship-project/tests
