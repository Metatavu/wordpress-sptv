/**
 * Interface describing SPTV ServiceLocationServiceChannelBlock component option
 */
export interface ServiceLocationServiceChannelBlockOptionComponent {
  slug: string,
  name: string
}

/**
 * Interface describing SPTV ServiceLocationServiceChannelBlock options
 */
export interface ServiceLocationServiceChannelBlockOptions {
  components: ServiceLocationServiceChannelBlockOptionComponent[]
}

/**
 * Interface describing SPTV ServiceBlock component option
 */
export interface ServiceBlockOptionComponent {
  slug: string,
  name: string
}

/**
 * Interface describing SPTV ServiceBlock options
 */
export interface ServiceBlockOptions {
  components: ServiceBlockOptionComponent[]
}

/**
 * Interface describing SPTV options
 */
export interface SptvOptions {
  serviceLocationServiceChannelBlock: ServiceLocationServiceChannelBlockOptions,
  serviceBlock: ServiceBlockOptions
}