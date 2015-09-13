package establishment;

import org.apache.lucene.document.Document;

import aux.Formatter;

public class Establishment {
	private Document doc;
	private double score;
	private GeographicCoordenates coordenates;
	private double distance;
	private Grade grade;
	
	public Establishment(Document doc, double score) {
		this.doc = doc; 
		this.score = score;
		this.coordenates = new GeographicCoordenates(doc);
	}

	public Document getDoc() {
		return doc;
	}

	public double getDistance() {
		return distance;
	}

	public double getScore() {
		return score;
	}

	public void setScore(double score) {
		this.score = score;
	}

	public GeographicCoordenates getCoordenates() {
		return coordenates;
	}

	public void setCoordenates(GeographicCoordenates coordenates) {
		this.coordenates = coordenates;
	}

	public void setDistance(double distance) {
		this.distance = distance;
	}

	public void setDistanceFrom(GeographicCoordenates origin) {
		this.distance = origin.getEuclidianDistance(this.coordenates);
	}
	
	public Grade getGrade() {
		return grade;
	}

	public void setGrade(Grade grade) {
		this.grade = grade;
	}
	
	@Override
	public String toString() {
		String line = this.getDoc().get("filename");
    line += " "+Formatter.formatDistance(this.getDistance())+"km";
    line += " score="+this.getScore();
		return line;
	}
}
