#!/bin/bash

echo "Testing central domain routes..."
curl -s http://localhost:8000/test

echo -e "\n\nTesting onboarding subdomain routes..."
curl -s -H "Host: onboarding.localhost:8000" http://localhost:8000/test

echo -e "\n\nTesting admin-panel subdomain routes..."
curl -s -H "Host: admin-panel.localhost:8000" http://localhost:8000/test 