<?hh // strict

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
class MHTTPResponse extends MObject {
	
	protected static bool $_responseSent = false;

	public static function setResponseSent(bool $responseSent) : void {
		MHTTPResponse::$_responseSent = $responseSent;
	}

	public static function responseSent() : bool {
		return MHTTPResponse::$_responseSent;
	}

	protected static function responseStringForCode(MHTTPResponseCode $code) : MString {
		if ($code == MHTTPResponseCode::Continue) {
			return S(MHTTPResponseName::Continue);
		} else if ($code == MHTTPResponseCode::SwitchingProtocols) {
			return S(MHTTPResponseName::SwitchingProtocols);
		} else if ($code == MHTTPResponseCode::Processing) {
			return S(MHTTPResponseName::Processing);
		} else if ($code == MHTTPResponseCode::OK) {
			return S(MHTTPResponseName::OK);
		} else if ($code == MHTTPResponseCode::Created) {
			return S(MHTTPResponseName::Created);
		} else if ($code == MHTTPResponseCode::Accepted) {
			return S(MHTTPResponseName::Accepted);
		} else if ($code == MHTTPResponseCode::NonAuthoritativeInformation) {
			return S(MHTTPResponseName::NonAuthoritativeInformation);
		} else if ($code == MHTTPResponseCode::NoContent) {
			return S(MHTTPResponseName::NoContent);
		} else if ($code == MHTTPResponseCode::ResetContent) {
			return S(MHTTPResponseName::NoContent);
		} else if ($code == MHTTPResponseCode::PartialContent) {
			return S(MHTTPResponseName::PartialContent);
		} else if ($code == MHTTPResponseCode::MultiStatus) {
			return S(MHTTPResponseName::MultiStatus);
		} else if ($code == MHTTPResponseCode::AlreadyReported) {
			return S(MHTTPResponseName::AlreadyReported);
		} else if ($code == MHTTPResponseCode::IMUsed) {
			return S(MHTTPResponseName::IMUsed);
		} else if ($code == MHTTPResponseCode::MultipleChoices) {
			return S(MHTTPResponseName::MultipleChoices);
		} else if ($code == MHTTPResponseCode::MovedPermanently) {
			return S(MHTTPResponseName::MovedPermanently);
		} else if ($code == MHTTPResponseCode::Found) {
			return S(MHTTPResponseName::Found);
		} else if ($code == MHTTPResponseCode::SeeOther) {
			return S(MHTTPResponseName::SeeOther);
		} else if ($code == MHTTPResponseCode::NotModified) {
			return S(MHTTPResponseName::NotModified);
		} else if ($code == MHTTPResponseCode::UseProxy) {
			return S(MHTTPResponseName::UseProxy);
		} else if ($code == MHTTPResponseCode::SwitchProxy) {
			return S(MHTTPResponseName::SwitchProxy);
		} else if ($code == MHTTPResponseCode::TemporaryRedirect) {
			return S(MHTTPResponseName::TemporaryRedirect);
		} else if ($code == MHTTPResponseCode::PermanentRedirect) {
			return S(MHTTPResponseName::PermanentRedirect);
		} else if ($code == MHTTPResponseCode::BadRequest) {
			return S(MHTTPResponseName::BadRequest);
		} else if ($code == MHTTPResponseCode::Unauthorized) {
			return S(MHTTPResponseName::Unauthorized);
		} else if ($code == MHTTPResponseCode::PaymentRequired) {
			return S(MHTTPResponseName::PaymentRequired);
		} else if ($code == MHTTPResponseCode::Forbidden) {
			return S(MHTTPResponseName::Forbidden);
		} else if ($code == MHTTPResponseCode::NotFound) {
			return S(MHTTPResponseName::NotFound);
		} else if ($code == MHTTPResponseCode::MethodNotAllowed) {
			return S(MHTTPResponseName::MethodNotAllowed);
		} else if ($code == MHTTPResponseCode::NotAcceptable) {
			return S(MHTTPResponseName::NotAcceptable);
		} else if ($code == MHTTPResponseCode::ProxyAuthenticationRequired) {
			return S(MHTTPResponseName::ProxyAuthenticationRequired);
		} else if ($code == MHTTPResponseCode::RequestTimeout) {
			return S(MHTTPResponseName::RequestTimeout);
		} else if ($code == MHTTPResponseCode::Conflict) {
			return S(MHTTPResponseName::Conflict);
		} else if ($code == MHTTPResponseCode::Gone) {
			return S(MHTTPResponseName::Gone);
		} else if ($code == MHTTPResponseCode::LengthRequired) {
			return S(MHTTPResponseName::LengthRequired);
		} else if ($code == MHTTPResponseCode::PreconditionFailed) {
			return S(MHTTPResponseName::PreconditionFailed);
		} else if ($code == MHTTPResponseCode::RequestEntityTooLarge) {
			return S(MHTTPResponseName::RequestEntityTooLarge);
		} else if ($code == MHTTPResponseCode::RequestURITooLong) {
			return S(MHTTPResponseName::RequestURITooLong);
		} else if ($code == MHTTPResponseCode::UnsupportedMediaType) {
			return S(MHTTPResponseName::UnsupportedMediaType);
		} else if ($code == MHTTPResponseCode::RequestedRangeNotSatisfiable) {
			return S(MHTTPResponseName::RequestRangeNotSatisfiable);
		} else if ($code == MHTTPResponseCode::ExpectationFailed) {
			return S(MHTTPResponseName::ExpectationFailed);
		} else if ($code == MHTTPResponseCode::ImATeapot) {
			return S(MHTTPResponseName::ImATeapot);
		} else if ($code == MHTTPResponseCode::EnhanceYourCalm) {
			return S(MHTTPResponseName::EnhanceYourCalm);
		} else if ($code == MHTTPResponseCode::UnprocessableEntity) {
			return S(MHTTPResponseName::UnprocessableEntity);
		} else if ($code == MHTTPResponseCode::Locked) {
			return S(MHTTPResponseName::Locked);
		} else if ($code == MHTTPResponseCode::FailedDependency) {
			return S(MHTTPResponseName::FailedDependency);
		} else if ($code == MHTTPResponseCode::UnorderedCollection) {
			return S(MHTTPResponseName::UnorderedCollection);
		} else if ($code == MHTTPResponseCode::UpgradeRequired) {
			return S(MHTTPResponseName::UpgradeRequired);
		} else if ($code == MHTTPResponseCode::PreconditionFailed) {
			return S(MHTTPResponseName::PreconditionFailed);
		} else if ($code == MHTTPResponseCode::TooManyRequests) {
			return S(MHTTPResponseName::TooManyRequests);
		} else if ($code == MHTTPResponseCode::RequestHeaderFieldsTooLarge) {
			return S(MHTTPResponseName::RequestHeaderFieldsTooLarge);
		} else if ($code == MHTTPResponseCode::NoResponse) {
			return S(MHTTPResponseName::NoResponse);
		} else if ($code == MHTTPResponseCode::RetryWith) {
			return S(MHTTPResponseName::RetryWith);
		} else if ($code == MHTTPResponseCode::BlockedByWindowsParentalControls) {
			return S(MHTTPResponseName::BlockedByWindowsParentalControls);
		} else if ($code == MHTTPResponseCode::ClientClosedRequest) {
			return S(MHTTPResponseName::ClientClosedRequest);
		} else if ($code == MHTTPResponseCode::InternalServerError) {
			return S(MHTTPResponseName::InternalServerError);
		} else if ($code == MHTTPResponseCode::NotImplemented) {
			return S(MHTTPResponseName::NotImplemented);
		} else if ($code == MHTTPResponseCode::BadGateway) {
			return S(MHTTPResponseName::BadGateway);
		} else if ($code == MHTTPResponseCode::ServiceUnavailable) {
			return S(MHTTPResponseName::ServiceUnavailable);
		} else if ($code == MHTTPResponseCode::GatewayTimeout) {
			return S(MHTTPResponseName::GatewayTimeout);
		} else if ($code == MHTTPResponseCode::HTTPVersionNotSupported) {
			return S(MHTTPResponseName::HTTPVersionNotSupported);
		} else if ($code == MHTTPResponseCode::VariantAlsoNegociates) {
			return S(MHTTPResponseName::VariantAlsoNegociates);
		} else if ($code == MHTTPResponseCode::InsufficientStorage) {
			return S(MHTTPResponseName::InsufficientStorage);
		} else if ($code == MHTTPResponseCode::LoopDetected) {
			return S(MHTTPResponseName::LoopDetected);
		} else if ($code == MHTTPResponseCode::BandwidthLimitExceeded) {
			return S(MHTTPResponseName::BandwidthLimitExceeded);
		} else if ($code == MHTTPResponseCode::NotExtended) {
			return S(MHTTPResponseName::NotExtended);
		} else if ($code == MHTTPResponseCode::NetworkAuthenticationRequired) {
			return S(MHTTPResponseName::NetworkAuthenticationRequired);
		} else if ($code == MHTTPResponseCode::NetworkReadTimeoutError) {
			return S(MHTTPResponseName::NetworkReadTimeoutError);
		} else if ($code == MHTTPResponseCode::NetworkConnectTimeoutError) {
			return S(MHTTPResponseName::NetworkConnectTimeoutError);
		}
	}
	
	//
	// ************************************************************
	//
	
	protected MHTTPResponseCode $_code;
	protected ?MString $_body;
	protected MMutableDictionary $_headers;
	
	/**
	 * 
	 *
	 * @return MHTTPResponse
	 */
	public function __construct(MHTTPResponseCode $code = MHTTPResponseCode::OK, ?MString $body = null) {
		parent::__construct();
		
		$this->_code = $code;
		$this->_body = $body;
		$this->_headers = new MMutableDictionary();
	}
	
	/******************** Properties ********************/
	
	public function setCode(MHTTPResponseCode $code) : void {
		$this->_code = $code;
	}
	
	public function code() : MHTTPResponseCode {
		return $this->_code;
	}
	
	public function setBody(?MString $body) : void {
		$this->_body = $body;
	}
	
	public function body() : ?MString {
		return $this->_body;
	}
	
	public function headers() : MDictionary {
		return (MDictionary)$this->_headers;
	}
	
	public function addHeader(MString $header, MString $value) : void {
		$this->_headers->setObjectForKey($header, $value);
	}
	
	public function removeHeader(MString $header) : void {
		$this->_headers->removeObjectForKey($header);
	}
	
	public function clearAllHeaders() : void {
		$this->_headers->removeAllObjects();
	}
	
	public function responseString() : MString {
		return MHTTPResponse::responseStringForCode($this->code());
	}
	
}

