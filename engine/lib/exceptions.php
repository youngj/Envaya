<?php

class IOException extends Exception {}
class ClassException extends Exception {}
class ConfigurationException extends Exception {}
class SecurityException extends Exception {}
class DatabaseException extends Exception {}
class APIException extends Exception {}
class CallException extends Exception {}
class DataFormatException extends Exception {}
class InvalidClassException extends ClassException {}
class ClassNotFoundException extends ClassException {}
class InstallationException extends ConfigurationException {}
class NotImplementedException extends CallException {}
class InvalidParameterException extends CallException {}
class RegistrationException extends InstallationException {}
class NotificationException extends Exception {}