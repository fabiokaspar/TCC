package core;
import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.DoubleField;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.StringField;
import org.apache.lucene.document.TextField;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.IndexWriterConfig;
import org.apache.lucene.index.IndexWriterConfig.OpenMode;
import org.apache.lucene.index.Term;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class IndexFiles 
{
	public static void main(String[] args)
	{
		try
		{
			String rootPath = "/var/www/TCC/colecao/restaurantes";
			String docsPath = rootPath + "/json";
			String indexPath = rootPath + "/index";
			
			Directory dir = FSDirectory.open(Paths.get(indexPath));
			Analyzer analyzer = new StandardAnalyzer();
			IndexWriterConfig iwc = new IndexWriterConfig(analyzer);
			iwc.setOpenMode(OpenMode.CREATE);
			IndexWriter writer = new IndexWriter(dir, iwc);
			final File folder = new File(docsPath);
			indexFolder(writer,folder);
			writer.close();
		}
		catch(Exception e)
		{
			System.out.println();
		}
	}
	static void indexDoc(IndexWriter writer, Path file) throws IOException {
		String descricao = "", link = "", nome = "";
		JSONArray latlon;
		try (InputStream stream = Files.newInputStream(file)) {
			Document doc = new Document();
      
			Field pathField = new StringField("path", file.toString(), Field.Store.YES);
			doc.add(pathField);
			Field filenameField = new StringField("filename", file.getFileName().toString(), Field.Store.YES);
			doc.add(filenameField);
			
			BufferedReader br = new BufferedReader(new InputStreamReader(stream, StandardCharsets.UTF_8));
			StringBuilder sb = new StringBuilder();
	    String line;
	    while ((line = br.readLine()) != null) {
	        sb.append(line);
	    }
	    try {
		    JSONObject json = new JSONObject(sb.toString());
		    nome = json.getString("nome");
	    	doc.add( new StringField("name", nome, Field.Store.YES) );
		    if(json.has("descricao")) {
		    	descricao = json.getString("descricao");
		    	doc.add( new TextField("contents", descricao, Field.Store.YES) );
		    }
		    link = json.getString("link");
		    doc.add( new StringField("link", link, Field.Store.YES) );
		    latlon = json.getJSONArray("latlon");
		    doc.add( new DoubleField("latitude", Double.parseDouble(latlon.get(0).toString()), Field.Store.YES) );
		    doc.add( new DoubleField("longitude", Double.parseDouble(latlon.get(1).toString()), Field.Store.YES) );
		    doc.add( new StringField("grade", json.getString("nota"), Field.Store.YES) );
	    } catch (JSONException e) {
	    	return;
	    }
			if (writer.getConfig().getOpenMode() == OpenMode.CREATE) {
				System.out.println("adding " + file);
				writer.addDocument(doc);
			} else {
				System.out.println("updating " + file);
				writer.updateDocument(new Term("path", file.toString()), doc);
			}
		}
    }
	private static void indexFolder(IndexWriter w, final File folder) throws IOException {
		String name;
		int indexOfDot;
		int counter = 0;
	    for (final File fileEntry : folder.listFiles()) {
	    	name = fileEntry.getName();
	    	indexOfDot = name.indexOf('.');
	    	if(indexOfDot > 0 && name.substring(indexOfDot).compareTo(".json") == 0) {
	    		indexDoc(w,fileEntry.toPath());
	    		counter++;
	    	}
	    }
	    System.out.println("\n\t"+counter+" files added.\n");
	}
}