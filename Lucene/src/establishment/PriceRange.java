package establishment;

public class PriceRange {
	private double minPrice;
	private double maxPrice;
	private double averagePrice;
	
	public PriceRange(double minPrice, double maxPrice) {
		this.minPrice = minPrice;
		this.maxPrice = maxPrice;
		this.averagePrice = (minPrice+maxPrice)/2;
	}

	public double getMinPrice() {
		return minPrice;
	}

	public void setMinPrice(double minPrice) {
		this.minPrice = minPrice;
	}

	public double getMaxPrice() {
		return maxPrice;
	}

	public void setMaxPrice(double maxPrice) {
		this.maxPrice = maxPrice;
	}

	public double getAveragePrice() {
		return averagePrice;
	}

	public void setAveragePrice(double averagePrice) {
		this.averagePrice = averagePrice;
	}
}
