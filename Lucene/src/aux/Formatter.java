package aux;

import java.text.DecimalFormat;

public abstract class Formatter {
	public static String formatDistance(Double dist) {
		DecimalFormat df2 = new DecimalFormat();
		return df2.format(dist);
	}
}
