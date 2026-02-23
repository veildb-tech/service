export default function Logo({ loginPage }) {
  const width = loginPage ? 120 : 135;
  const height = loginPage ? 70 : 49;

  return (
    <img
      src="/assets/logo.png"
      alt="Logo"
      width={width}
      height={height}
    />
  );
}
