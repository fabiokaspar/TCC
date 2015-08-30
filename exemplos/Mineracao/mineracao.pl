#!/usr/bin/perl

use utf8;
use LWP::Simple;
use Image::Grab;
use File::Copy qw(copy);
binmode(STDOUT, ":utf8");
	
my $query = 'Restaurante';
my $url_base = "http://www.google.com/search?";
my $dados = {
  'start'=>0,
  'num'=>10,
  'q'=>"red+sox",
	'cr'=>"countryCA",
  'lr'=>"lang_fr",
  'client'=>"google-csbe",
  'output'=>"xml_no_dtd",
  'cx'=>"00255077836266642015:u-scht7a-8i"
};
my @gets = ();
while ( my ($key, $value) = each(%dados) ) {
    push(@gets,"$key=$value");
}
$gets_str = join("&",@gets);
my $url = $url_base.$gets_str;

for (my $index=1; $index <= 1; $index++) {
	my $content = get $url;
	die "Couldn't get $url" unless defined $content;
	open FILE, ">teste.html";
	print FILE $content;
}


 

