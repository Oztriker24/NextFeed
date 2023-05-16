export default function removeTailingSlash({ path, query, hash }) {
  if (path === "/" || !path.endsWith("/")) return;

  const nextPath = path.replace(/\/+$/, "") || "/";
  const nextRoute = { path: nextPath, query, hash };

  return navigateTo(nextRoute, { redirectCode: 308 });
}