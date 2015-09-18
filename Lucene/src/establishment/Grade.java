package establishment;

public enum Grade {
	RUIM,REGULAR,BOM,MUITO_BOM,OTIMO;
	
	public static Grade getEnum(String str) {
		if(str.compareToIgnoreCase("ótimo") == 0 ||
		   str.compareToIgnoreCase("otimo") == 0) {
			return OTIMO;
		}
		if(str.compareToIgnoreCase("muito bom") == 0) {
			return MUITO_BOM;
		}
		if(str.compareToIgnoreCase("bom") == 0) {
			return BOM;
		}
		if(str.compareToIgnoreCase("regular") == 0) {
			return REGULAR;
		}
		if(str.compareToIgnoreCase("ruim") == 0) {
			return RUIM;
		}
        throw new IllegalArgumentException();
    }
	
}
