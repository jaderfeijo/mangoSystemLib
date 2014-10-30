<?hh

/*
 * Copyright (c) 2011 Movinpixel Ltd. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. Neither the name of the company nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY MOVINPIXEL AND ITS CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL MOVINPIXEL OR ITS CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */

/**
 * @author Jader Feijo <jader@movinpixel.com>
 *
 * @license MIT
 */

/******************** Types ********************/

type MManagedObjectCallback = (function(MManagedObject) : void);
type MInitializerCallback<T> = (function() : T);
type MInitWithCallback<Tw, To> = (function(Tw) : To);
type MInitWithoutCallback<T> = (function() : T);
type MExecuteCallback<T> = (function(T) : void);
type MNullCallback = (function() : void);

/******************** Enums ********************/

enum MArrayOrder : int {
	Ascending = 0;
	Descending = 1;
}

enum MDateFormat : int {
	Short = 0;
	Full = 1;
}

enum MHourFormat : int {
	TwentyFourHour = 0;
	TwelveHour = 1;
}

enum MHTTPRequestMethod : string {
	OPTIONS = 'OPTIONS';
	GET = 'GET';
	HEAD = 'HEAD';
	POST = 'POST';
	PUT = 'PUT';
	DELETE = 'DELETE';
	TRACE = 'TRACE';
	CONNECT = 'CONNECT';
}

enum MApplicationControllerAttributeType : int {
	String = 0;
	Integer = 1;
	Float = 2;
	Boolean = 3;
	Date = 4;
	Binary = 5;
	Array = 6;
	Dictionary = 7;
}

enum MComparisonResult : int {
	Descending = -1;
	Same = 0;
	Ascending = 1;
}

enum MHTMLHeaderType : int {
	Type1 = 1;
	Type2 = 2;
	Type3 = 3;
	Type4 = 4;
	Type5 = 5;
	Type6 = 6;
}

enum MPropertyType : string {
	String = 'String';
	Integer = 'Integer';
	Float = 'Float';
	Boolean = 'Boolean';
	Date = 'Date';
	Binary = 'Binary';
	Unknown = 'Unknown';
}

enum MRelationshipTo : int {
	One = 0;
	Many = 1;
}

enum MRelationshipKind : int {
	Unknown = 0;
	OneToOne = 1;
	OneToMany = 2;
	OneToNone = 3;
	ManyToMany = 4;
	ManyToNone = 5;
}

enum MEntityProviderOutputType : int {
	Xml = 0;
	Json = 1;
}

enum MHTTPResponseCode : int {
	Continue = 100;
	SwitchingProtocols = 101;
	Processing = 102;
	OK = 200;
	Created = 201;
	Accepted = 202;
	NonAuthoritativeInformation = 203;
	NoContent = 204;
	ResetContent = 205;
	PartialContent = 206;
	MultiStatus = 207;
	AlreadyReported = 208;
	IMUsed = 226;
	MultipleChoices = 300;
	MovedPermanently = 301;
	Found = 302;
	SeeOther = 303;
	NotModified = 304;
	UseProxy = 305;
	SwitchProxy = 306;
	TemporaryRedirect = 307;
	PermanentRedirect = 308;
	BadRequest = 400;
	Unauthorized = 401;
	PaymentRequired = 402;
	Forbidden = 403;
	NotFound = 404;
	MethodNotAllowed = 405;
	NotAcceptable = 406;
	ProxyAuthenticationRequired = 407;
	RequestTimeout = 408;
	Conflict = 409;
	Gone = 410;
	LengthRequired = 411;
	PreconditionFailed = 412;
	RequestEntityTooLarge = 413;
	RequestURITooLong = 414;
	UnsupportedMediaType = 415;
	RequestRangeNotSatisfiable = 416;
	ExpectationFailed = 417;
	ImATeapot = 418;
	EnhanceYourCalm = 420;
	UnprocessableEntity = 422;
	Locked = 423;
	FailedDependency = 424;
	UnorderedCollection = 425;
	UpgradeRequired = 426;
	PreconditionRequired = 428;
	TooManyRequests = 429;
	RequestHeaderFieldsTooLarge = 431;
	NoResponse = 444;
	RetryWith = 449;
	BlockedByWindowsParentalControls = 450;
	ClientClosedRequest = 499;
	InternalServerError = 500;
	NotImplemented = 501;
	BadGateway = 502;
	ServiceUnavailable = 503;
	GatewayTimeout = 504;
	HTTPVersionNotSupported = 505;
	VariantAlsoNegociates = 506;
	InsufficientStorage = 507;
	LoopDetected = 508;
	BandwidthLimitExceeded = 509;
	NotExtended = 510;
	NetworkAuthenticationRequired = 511;
	NetworkReadTimeoutError = 598;
	NetworkConnectTimeoutError = 599;
}

enum MHTTPResponseName : string {
	Continue = "Continue";
	SwitchingProtocols = "Switching Protocols";
	Processing = "Processing";
	OK = "OK";
	Created = "Created";
	Accepted = "Accepted";
	NonAuthoritativeInformation = "Non-Authoritative Information";
	NoContent = "No Content";
	ResetContent = "Reset Content";
	PartialContent = "Partial Content";
	MultiStatus = "Multi-Status";
	AlreadyReported = "Already Reported";
	IMUsed = "IM Used";
	MultipleChoices = "Multiple Choices";
	MovedPermanently = "Moved Permanently";
	Found = "Found";
	SeeOther = "See Other";
	NotModified = "Not Modified";
	UseProxy = "Use Proxy";
	SwitchProxy = "Switch Proxy";
	TemporaryRedirect = "Temporary Redirect";
	PermanentRedirect = "Permanent Redirect";
	BadRequest = "Bad Request";
	Unauthorized = "Unauthorized";
	PaymentRequired = "Payment Required";
	Forbidden = "Forbidden";
	NotFound = "Not Found";
	MethodNotAllowed = "Method Not Allowed";
	NotAcceptable = "Not Acceptable";
	ProxyAuthenticationRequired = "Proxy Authentication Required";
	RequestTimeout = "Request Timeout";
	Conflict = "Conflict";
	Gone = "Gone";
	LengthRequired = "Length Required";
	PreconditionFailed = "Precondition Failed";
	RequestEntityTooLarge = "Request Entity Too Large";
	RequestURITooLong = "Request-URI Too Long";
	UnsupportedMediaType = "Unsupported Media Type";
	RequestedRangeNotSatisfiable = "Requested Range Not Satisfiable";
	ExpectationFailed = "Expectation Failed";
	ImATeapot = "I'm a teapot";
	EnhanceYourCalm = "Enhance Your Calm";
	UnprocessableEntity = "Unprocessable Entity";
	Locked = "Locked";
	FailedDependency = "Failed Dependency";
	UnorderedCollection = "Unordered Collection";
	UpgradeRequired = "Upgrade Required";
	PreconditionRequired = "Precondition Required";
	TooManyRequests = "Too Many Requests";
	RequestHeaderFieldsTooLarge = "Request Header Fields Too Large";
	NoResponse = "No Response";
	RetryWith = "Retry With";
	BlockedByWindowsParentalControls = "Blocked by Windows Parental Controls";
	ClientClosedRequest = "Client Closed Request";
	InternalServerError = "Internal Server Error";
	NotImplemented = "Not Implemented";
	BadGateway = "Bad Gateway";
	ServiceUnavailable = "Service Unavailable";
	GatewayTimeout = "Gateway Timeout";
	HTTPVersionNotSupported = "HTTP Version Not Supported";
	VariantAlsoNegociates = "Variant Also Negociates";
	InsufficientStorage = "Insufficient Storage";
	LoopDetected = "Loop Detected";
	BandwidthLimitExceeded = "Bandwidth Limit Exceeded";
	NotExtended = "Not Extended";
	NetworkAuthenticationRequired = "Network Authentication Required";
	NetworkReadTimeoutError = "Netowrk read timeout error";
	NetworkConnectTimeoutError = "Network connect timeout error";
}

/******************** Utility Functions ********************/

function MStringFromPropertyType(MPropertyType $type) : MString {
	if ($type == MPropertyType::String) {
		return S("String");
	} else if ($type == MPropertyType::Integer) {
		return S("Integer");
	} else if ($type == MPropertyType::Float) {
		return S("Float");
	} else if ($type == MPropertyType::Boolean) {
		return S("Boolean");
	} else if ($type == MPropertyType::Date) {
		return S("Date");
	} else if ($type == MPropertyType::Binary) {
		return S("Binary");
	} else {
		return S("Unknown");
	}
}

function MPropertyTypeFromString(MString $type) : MPropertyType {
	if ($type->equals(S("String"))) {
		return MPropertyType::String;
	} else if ($type->equals(S("Integer"))) {
		return MPropertyType::Integer;
	} else if ($type->equals(S("Float"))) {
		return MPropertyType::Float;
	} else if ($type->equals(S("Boolean"))) {
		return MPropertyType::Boolean;
	} else if ($type->equals(S("Date"))) {
		return MPropertyType::Date;
	} else if ($type->equals(S("Binary"))) {
		return MPropertyType::Binary;
	} else {
		return MPropertyType::Unknown;	
	}
}
