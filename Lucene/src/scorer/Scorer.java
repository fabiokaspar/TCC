package scorer;

import establishment.Establishment;
import establishment.Establishments;
import establishment.Grade;

public class Scorer {
	public static final int CURRENT = 0;
	public static final int DISTANCE = 1;
	public static final int GRADE = 2;
	public static final int PRICE = 3;
	public static final int N_OPT = 4;
	
	private double normalizer;
	private double maxDistance;
	private Grade maxGrade;
	private double maxPrice;
	
	public Scorer(double norm, double maxDistance, Grade maxGrade, double maxPrice) {
		this.normalizer = norm;
		this.maxDistance = maxDistance;
		this.maxGrade = maxGrade;
		this.maxPrice = maxPrice;
	}
	
	public Scorer(Establishments establishments) {
		this.normalizer = establishments.getMaxScore();
		this.maxDistance = establishments.getMaxDistance();
		this.maxGrade = Grade.OTIMO;
		this.maxPrice = 0;
	}
	
	public Establishments calculateScore(Establishments establishments, int[] weights) {
		int currentWeight  = weights[CURRENT];
	  int distanceWeight = weights[DISTANCE];
	  int gradeWeight    = weights[GRADE];
	  int priceWeight    = weights[PRICE];
	  return calculateScore(establishments, currentWeight, distanceWeight, gradeWeight);
	}
	
	public Establishments calculateScore(Establishments establishments, int currentWeight,
			int distanceWeight, int gradeWeight) {
		int sumOfWeights = currentWeight + distanceWeight + gradeWeight;
		double currentContribution  = ((double)currentWeight)/sumOfWeights;
		double distanceContribution = ((double)distanceWeight)/sumOfWeights;
		double gradeContribution    = ((double)gradeWeight)/sumOfWeights;
		
		for (Establishment establishment : establishments) {
			double currentParcel  = currentContribution  * establishment.getScore();
			double distanceParcel = distanceContribution * calculateDistanceScore(establishment);
			double gradeParcel    = gradeContribution    * calculateGradeScore(establishment);
			double newScore = currentParcel + distanceParcel + gradeParcel;
			establishment.setScore(newScore);
		}
		return establishments;
	}
	
	private double calculateDistanceScore(Establishment e) {
		return 1.0 - (e.getDistance()/this.maxDistance);
	}
	private double calculateGradeScore(Establishment e) {
		return ((double)e.getGrade().ordinal())/this.maxGrade.ordinal(); 
	}
}
