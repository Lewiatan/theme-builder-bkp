import { type RouteConfig, index, route } from "@react-router/dev/routes";

export default [
  index("routes/home.tsx"),
  route("shop/:shopId", "routes/shop.$shopId.tsx"),
] satisfies RouteConfig;
