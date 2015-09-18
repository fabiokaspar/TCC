package tests;

import static org.junit.Assert.*;

import org.junit.Test;

import establishment.GeographicCoordenates;

public class GeographicCoordenatesTest {

	@Test
	public void test() {
		GeographicCoordenates origin = new GeographicCoordenates(-23.5326751, -46.6960534);
		GeographicCoordenates destiny = new GeographicCoordenates(-23.5657364, -46.6657161);
		double dist = origin.getEuclidianDistance(destiny);
		assertEquals(4.811, dist, 0.01);
	}

}
