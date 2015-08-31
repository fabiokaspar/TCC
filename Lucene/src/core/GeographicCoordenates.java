package core;

public class GeographicCoordenates {
	private double latitude;
	private double longitude;
	
	public GeographicCoordenates(double latitude, double longitude) {
		this.latitude = latitude;
		this.longitude = longitude;
	}

	public double getLatitude() {
		return latitude;
	}

	public void setLatitude(double latitude) {
		this.latitude = latitude;
	}

	public double getLongitude() {
		return longitude;
	}

	public void setLongitude(double longitude) {
		this.longitude = longitude;
	}
	
	public double getEuclidianDistance(GeographicCoordenates otherPlace) {
	  double lat1 = this.latitude, lon1 = this.longitude;
	  double lat2 = otherPlace.latitude, lon2 = otherPlace.longitude;
		
	  double theta = lon1 - lon2;
	  double dist = Math.sin(deg2rad(lat1)) * Math.sin(deg2rad(lat2)) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.cos(deg2rad(theta));
	  dist = Math.acos(dist);
	  dist = rad2deg(dist);
	  dist = dist * 60 * 1.1515 * 1.609344;
	  return (dist);
	}
	private double deg2rad(double deg) {
	  return (deg * Math.PI / 180.0);
	}
	private double rad2deg(double rad) {
	  return (rad * 180 / Math.PI);
	}

	@Override
	public String toString() {
		return "Lat:"+this.latitude+" Lng:"+this.longitude;
	}
}
