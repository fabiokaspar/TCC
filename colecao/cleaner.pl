#!/usr/bin/perl

use utf8;
use Data::Dumper;

my $filename = $ARGV[0];
print $filename."\n";

local $/ = undef;
open (my $file, "<", $filename)	or die 0;
binmode FILE;
my $text = <$file>;
close($file);

my %args = ();
if($text =~ /\<\!\-{2}TITLE\-{2}\>(.+?)\<\!\-{2}\/LOCATIONS\-{2}\>/s) {
	$text = $1;
	if($text =~ /alt\=\"(\w+)\"/s) {
		$args{'nota'} = $1;
	}
	if($text =~ /\<td class\=\"localInfo\" colspan\=\"2\"\>\n(.+)Telefone/s) {
		$args{'endereço'} = $1;
	}


	for(keys %args) {
		print "$_ : $args{$_}\n";
	}
	print "OK";
} else {
	print "--";
}
print "\n\n";


