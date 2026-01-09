import { NextResponse } from 'next/server';

// This function can be marked `async` if using `await` inside
export function middleware(request) {
  request.cookies.get('workspace');
  request.cookies.get('jwt');

  // if (
  //   jwt &&
  //   !request.nextUrl.pathname.startsWith("/auth/") &&
  //   !request.nextUrl.pathname.startsWith("/account")
  // ) {
  //   if (!currentWorkspace) {
  //     return NextResponse.redirect(new URL("/auth/workspace", request.url));
  //   } else {
  //     // If url without workspace code then redirect to home page with workspace code
  //     if (
  //       !request.nextUrl.pathname.startsWith("/" + currentWorkspace.value) ||
  //       request.nextUrl.pathname === "/"
  //     ) {
  //       return NextResponse.redirect(new URL("/" + currentWorkspace.value, request.url));
  //     }
  //   }
  // }

  if (request.nextUrl.pathname === '/auth/restore-password'
    && !request.nextUrl.searchParams.get('hash')
  ) {
    return NextResponse.redirect(new URL('/auth/forgot-password', request.url));
  }
}

export const config = {
  matcher: [
    /*
     * Match all request paths except for the ones starting with:
     * - api (API routes)
     * - _next/static (static files)
     * - _next/image (image optimization files)
     * - favicon.ico (favicon file)
     */
    '/((?!api|_next/static|assets|_next/image|favicon.ico).*)',
  ],
};
