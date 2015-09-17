package establishment;

import java.util.ArrayList;
import java.util.Comparator;

public class Establishments extends ArrayList<Establishment> {
	private static final long serialVersionUID = 1L;
	
	public Establishments() {
		super();
	}
	
	public void sort() {
		java.util.Collections.sort(this, new Comparator<Establishment>() {
			@Override
			public int compare(Establishment o1, Establishment o2) {
				if(o1.getScore() > o2.getScore()) {
					return -1;
				} else if(o1.getScore() < o2.getScore()) {
					return 1;
				}
				return 0;
			}
		});
	}
	
	public double getMaxDistance() {
		double max = 0.0;
		for (Establishment e : this) {
			if(e.getDistance() > max) {
				max = e.getDistance();
			}
		}
		return max;
	}
	
	public double getMaxScore() {
		double max = 0.0;
		for (Establishment e : this) {
			if(e.getScore() > max) {
				max = e.getScore();
			}
		}
		return max;
	}
	
	public double getMaxPrice() {
		double max = 0.0;
		for (Establishment e : this) {
			if(e.getPriceRange().getMaxPrice() > max) {
				max = e.getPriceRange().getMaxPrice();
			}
		}
		return max;
	}
}
