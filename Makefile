test:
	vendor\bin\pest

test-cover:
	vendor\bin\pest --coverage

pint:
	vendor\bin\pint


test-coverage:
	vendor\bin\pest --coverage

test-help:
	vendor\bin\pest --help


stan:
	vendor\bin\phpstan analyze --configuration phpstan.neon --memory-limit=4G --xdebug

stan-debug:
	vendor\bin\phpstan analyze --configuration phpstan.neon --memory-limit=4G --debug --xdebug

stan-base:
	vendor\bin\phpstan analyze --configuration phpstan.neon --memory-limit=4G --debug --generate-baseline --xdebug

stan-help:
	vendor\bin\phpstan --help



rector:
	vendor\bin\rector
# vendor\bin\rector --clear-cache

rector-dry:
	vendor\bin\rector --dry-run

rector-help:
	vendor\bin\rector --help


all:
	vendor\bin\pint
	vendor\bin\pest
	vendor\bin\phpstan analyze --configuration phpstan.neon --memory-limit=4G --debug
	vendor\bin\rector
