package scorer;

import establishment.Establishment;
import establishment.Establishments;
import establishment.Grade;

public class Scorer {
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
	
	public Establishments calculateScore(Establishments establishments, int currentWeight,
			int distanceWeight) {
		int sumOfWeights = currentWeight + distanceWeight;
		double currentContribution = ((double)currentWeight)/sumOfWeights;
		double distanceContribution = ((double)distanceWeight)/sumOfWeights;
		for (Establishment establishment : establishments) {
			double currentParcel = currentContribution * establishment.getScore();
			double distanceParcel = distanceContribution * calculateDistanceScore(establishment);
			double newScore = currentParcel + distanceParcel;
			establishment.setScore(newScore);
		}
		return establishments;
	}
	
	private double calculateDistanceScore(Establishment e) {
		return 1.0 - (e.getDistance()/this.maxDistance);
	}
}
