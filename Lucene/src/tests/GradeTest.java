package tests;

import static org.junit.Assert.*;

import org.junit.Test;

import establishment.Grade;

public class GradeTest {

	@Test
	public void comparisonTest() {
		assertTrue(Grade.OTIMO.compareTo(Grade.MUITO_BOM) > 0);
		assertFalse(Grade.BOM.compareTo(Grade.MUITO_BOM) > 0);
	}
	
	@Test
	public void getEnumTest() {
		assertEquals(Grade.getEnum("muito bom"), Grade.MUITO_BOM);
		assertEquals(Grade.getEnum("ótimo"), Grade.OTIMO);
		assertEquals(Grade.getEnum("Ótimo"), Grade.OTIMO);
	}

	@Test
	public void getEnumCleanTest() {
		assertEquals(Grade.getEnum("otimo"), Grade.OTIMO);
	}
}
