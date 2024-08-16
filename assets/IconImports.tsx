// Exporting Icons from IconImport.tsx
import { Feather, MaterialIcons  } from "@expo/vector-icons";

const IconImports = {
  Location: (props: any) => <Feather name="map-pin" {...props} />,
  RightArrow: (props: any) => <MaterialIcons name="arrow-forward-ios" {...props} />,


};

export { IconImports };