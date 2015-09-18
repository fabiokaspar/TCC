import java.net.MalformedURLException;
import java.net.URL;
import java.util.List;

import org.dom4j.Document;
import org.dom4j.DocumentException;
import org.dom4j.Element;
import org.dom4j.io.SAXReader;

public class CalculadorDeDistancias {

	public static String putSpaces(String s) {
		return s.replace(' ', '+');
	}

	public static int calcularDistancia(String origem, String destino) {
		try {
			URL url;
			url = new URL(
					"http://maps.google.es/maps/api/directions/xml?origin="
							+ putSpaces(origem) + "&destination="
							+ putSpaces(destino) + "&sensor=false");
			String path = "//DirectionsResponse/route/leg/distance/text";
			String result = extraiInfoDoXML(url, path);
			return processaDistancia(result);
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}
		return 0;
	}

	private static int processaDistancia(String result) {
		int r = 0;
		if (result.matches("\\d+(,\\d+)? km")) {
			result = result.replace(" km", "");
			result = result.replace(",", ".");
			r = (int) (Double.parseDouble(result) * 1000);
		} else if (result.matches("\\d+ m")) {
			result = result.replace(" m", "");
			r = Integer.parseInt(result);
		}
		return r;
	}

	public static int getLatFromAdress(String adress) {
		URL url;
		try {
			url = new URL(
					"http://maps.googleapis.com/maps/api/geocode/xml?address="
							+ putSpaces(adress)
							+ ",+Sao+Paulo,+SP,+Brasil&sensor=false");
			String path = "//GeocodeResponse/result/geometry/location/lat";
			Double lat = Double.parseDouble(extraiInfoDoXML(url, path));
			return (int) (lat * 100);
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}
		return 0;
	}

	public static int getLngFromAdress(String adress) {
		URL url;
		try {
			url = new URL(
					"http://maps.googleapis.com/maps/api/geocode/xml?address="
							+ putSpaces(adress)
							+ ",+Sao+Paulo,+SP,+Brasil&sensor=false");
			String path = "//GeocodeResponse/result/geometry/location/lng";
			Double lng = Double.parseDouble(extraiInfoDoXML(url, path));
			return (int) (lng * 100);
		} catch (MalformedURLException e) {
			e.printStackTrace();
		}
		return 0;
	}

	@SuppressWarnings("rawtypes")
	private static String extraiInfoDoXML(URL url, String path) {
		try {
			SAXReader reader = new SAXReader();
			Document document = reader.read(url);
			List list = document.selectNodes(path);
			if (!list.isEmpty()) {
				Element element = (Element) list.get(list.size() - 1);
				return element.getText();
			}
		} catch (DocumentException e) {
			e.printStackTrace();
		}
		return "";
	}
}
