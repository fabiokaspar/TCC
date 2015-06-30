#!/usr/bin/perl

use utf8;
use LWP::Simple;
use Image::Grab;
use File::Copy qw(copy);
binmode(STDOUT, ":utf8");
	
my $query = 'Restaurante';
my $url_base = "http://www.google.com/search?start=0&num=10&q=red&cr=countryCA&lr=lang_fr&client=google-csbe&output=xml_no_dtd&cx=00255077836266642015:u-scht7a-8i";

for (my $index=1; $index <= 1; $index++) {
	if($index == 1) {
		$url = $url_base."";
	} else {
		$url = $url_base."";
	}
	my $content = get $url;
	die "Couldn't get $url" unless defined $content;
	print $content;
	my @matches = ($content =~ m/<div class="([A-Za-z-]*)"/g);
	print "$index\n";
	while(<@matches>) {
		#print "\t$_\n";
	}
}


 

