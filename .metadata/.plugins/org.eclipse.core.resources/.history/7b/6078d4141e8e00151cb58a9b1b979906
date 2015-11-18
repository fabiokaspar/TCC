package core;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.nio.charset.StandardCharsets;
import java.nio.file.Paths;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.DirectoryReader;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.queryparser.classic.QueryParser;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.store.FSDirectory;

import scorer.Scorer;
import establishment.Establishment;
import establishment.Establishments;
import establishment.GeographicCoordenates;

public class SearchFiles {

	private static final String index = "/var/www/TCC/colecao/restaurantes/index";
  private SearchFiles() {}

  public static void main(String[] args) throws Exception {
    if (args.length > 0 && ("-h".equals(args[0]) || "-help".equals(args[0]))) {
      System.out.println("Usage:\tjava core.SearchFiles [-query string] [-lat double] [-lng double] [-distance] [-grade] [-price]");
      System.exit(0);
    }

    String field = "contents";
    String queryString = null;
    GeographicCoordenates origin = new GeographicCoordenates(-23.5505199,-46.6333094);
    int[] weights = new int[Scorer.N_OPT];
    weights[Scorer.CURRENT] = 1;
    
    for(int i = 0;i < args.length;i++) {
      if ("-query".equals(args[i])) {
        queryString = args[i+1];
        i++;
      } else if ("-lat".equals(args[i])) {
	    	origin.setLatitude(Double.parseDouble(args[i+1]));
	    	i++;
      } else if ("-lng".equals(args[i])) {
	    	origin.setLongitude(Double.parseDouble(args[i+1]));
	    	i++;
      } else if ("-distance".equals(args[i])) {
      	adjustWeightArray(weights, Scorer.DISTANCE);
      } else if ("-grade".equals(args[i])) {
      	adjustWeightArray(weights, Scorer.GRADE);
      } else if ("-price".equals(args[i])) {
      	adjustWeightArray(weights, Scorer.PRICE);
      }
    }
   
    IndexReader reader = DirectoryReader.open(FSDirectory.open(Paths.get(index)));
    IndexSearcher searcher = new IndexSearcher(reader);
    Analyzer analyzer = new StandardAnalyzer();
    QueryParser parser = new QueryParser(field, analyzer);
    BufferedReader in = new BufferedReader(new InputStreamReader(System.in, StandardCharsets.UTF_8));
    String line = "";
    if (queryString == null) {
		  System.out.println("Enter query: ");
		  line = in.readLine();
		} else {
			line = queryString;
		}
		line = line.trim();
      
    Query query = parser.parse(line);
    Establishments establishments = doSearch(searcher, query, origin);
    
    Scorer scorer = new Scorer(establishments);
    establishments = scorer.calculateScore(establishments, weights);
    
    establishments.sort();
    for (Establishment establishment : establishments) {
	  	System.out.println(establishment);                  
	  }
    //System.out.println(numTotalHits + " total matching documents");
    reader.close();
  }
  
  public static Establishments doSearch(IndexSearcher searcher, Query query, GeographicCoordenates origin) throws IOException {
  	int maxNumberOfDocs = 100; // Arbitrary number
    TopDocs results = searcher.search(query, maxNumberOfDocs);
    ScoreDoc[] hits = results.scoreDocs;
    int numTotalHits = results.totalHits; 
    if(results.totalHits == 0) {
    	return new Establishments();
    }

    Establishments establishments = new Establishments();
	  for (int i = 0; i < numTotalHits; i++) {
	    Document doc = searcher.doc(hits[i].doc);
	    String path = doc.get("filename");
	    if (path == null) {
	      continue;
	    }
	    Establishment e = new Establishment(doc,hits[i].score);
	    e.setDistanceFrom(origin);
	    establishments.add(e);
	  }
	  return establishments;
  }
  
  private static void adjustWeightArray(int[] weights, int index) {
  	weights[index] = 2;
  	for(int i = 0; i < weights.length; i++) {
  		if(i == index) {
  			continue;
  		}
  		if(weights[i] > 0) {
  			weights[i]++;
  		}
  	}
  }
}